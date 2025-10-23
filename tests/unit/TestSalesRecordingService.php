<?php

use PHPUnit\Framework\TestCase;

class TestSalesRecordingService extends TestCase {
    
    private $sales_recording_service;
    private $sales_service;
    private $band_id;
    private $merch_id;
    
    public function setUp(): void {
        parent::setUp();
        
        // Initialize services
        $this->sales_recording_service = new Merchmanager_Sales_Recording_Service();
        $this->sales_service = new Merchmanager_Sales_Service();
        
        // Create test data
        $this->band_id = MSP_Test_Utils::create_test_band();
        $this->merch_id = MSP_Test_Utils::create_test_merchandise($this->band_id);
        
        // Ensure database tables exist
        $database = new Merchmanager_Database();
        if (!$database->tables_exist()) {
            $database->create_tables();
        }
        
        // Clear any existing sales session
        $this->sales_recording_service->clear_sales_session();
    }
    
    public function test_add_to_sales_session() {
        global $wpdb;
        
        // Add item to sales session
        $result = $this->sales_recording_service->add_to_sales_session($this->merch_id, 2);
        
        // Verify success
        $this->assertTrue($result);
        
        // Verify sales session contains the item
        $sales_session = $this->sales_recording_service->get_sales_session();
        $this->assertArrayHasKey($this->merch_id, $sales_session);
        $this->assertEquals(2, $sales_session[$this->merch_id]['quantity']);
        
        // Verify sales items with details
        $sales_items = $this->sales_recording_service->get_sales_items();
        $this->assertCount(1, $sales_items);
        $this->assertEquals($this->merch_id, $sales_items[0]['merchandise_id']);
        $this->assertEquals(2, $sales_items[0]['quantity']);
        $this->assertEquals(50.00, $sales_items[0]['subtotal']); // 2 * 25.00
    }
    
    public function test_validate_sales_session() {
        // Add item to sales session
        $this->sales_recording_service->add_to_sales_session($this->merch_id, 3);
        
        // Validate sales session
        $validation = $this->sales_recording_service->validate_sales_session();
        
        // Verify validation passes
        $this->assertEmpty($validation['errors']);
        $this->assertArrayHasKey($this->merch_id, $validation['valid_items']);
        $this->assertEquals(3, $validation['valid_items'][$this->merch_id]['quantity']);
    }
    
    public function test_process_sale_recording() {
        global $wpdb;
        
        // Add item to sales session
        $this->sales_recording_service->add_to_sales_session($this->merch_id, 1);
        
        // Process sale recording
        $sale_data = array(
            'payment_type' => 'cash',
            'show_id' => 0,
            'notes' => 'Test sale'
        );
        
        $result = $this->sales_recording_service->process_sale_recording($sale_data);
        
        // Verify success
        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEquals('Sale recorded successfully.', $result['message']);
        
        // Verify sale was recorded in database
        $sales_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}msp_sales");
        $this->assertEquals(1, $sales_count);
        
        // Verify sale details
        $sale = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}msp_sales WHERE merchandise_id = {$this->merch_id}");
        $this->assertEquals($this->merch_id, $sale->merchandise_id);
        $this->assertEquals(1, $sale->quantity);
        $this->assertEquals(25.00, $sale->price);
        $this->assertEquals('cash', $sale->payment_type);
        $this->assertEquals($this->band_id, $sale->band_id);
        $this->assertEquals('Test sale', $sale->notes);
        
        // Verify stock was updated
        $stock = get_post_meta($this->merch_id, '_msp_merchandise_stock', true);
        $this->assertEquals(99, $stock); // Original 100 - 1 sold
        
        // Verify stock log was created
        $stock_logs_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}msp_stock_log WHERE merchandise_id = {$this->merch_id}");
        $this->assertEquals(1, $stock_logs_count);
        
        // Verify sales session was cleared
        $sales_session = $this->sales_recording_service->get_sales_session();
        $this->assertEmpty($sales_session);
    }
    
    public function test_process_sale_recording_with_show() {
        global $wpdb;
        
        // Create test show
        $tour_id = MSP_Test_Utils::create_test_tour($this->band_id);
        $show_id = MSP_Test_Utils::create_test_show($tour_id);
        
        // Add item to sales session
        $this->sales_recording_service->add_to_sales_session($this->merch_id, 2);
        
        // Process sale recording with show
        $sale_data = array(
            'payment_type' => 'card',
            'show_id' => $show_id,
            'notes' => 'Show sale'
        );
        
        $result = $this->sales_recording_service->process_sale_recording($sale_data);
        
        // Verify success
        $this->assertTrue($result['success']);
        
        // Verify sale was recorded with show ID
        $sale = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}msp_sales WHERE merchandise_id = {$this->merch_id}");
        $this->assertEquals($show_id, $sale->show_id);
        $this->assertEquals('card', $sale->payment_type);
    }
    
    public function test_process_sale_recording_multiple_items() {
        global $wpdb;
        
        // Create second merchandise item
        $merch_id2 = MSP_Test_Utils::create_test_merchandise($this->band_id, array(
            'post_title' => 'Test Merch 2',
            'meta_input' => array(
                '_msp_merchandise_price' => 15.00,
                '_msp_merchandise_stock' => 50
            )
        ));
        
        // Add multiple items to sales session
        $this->sales_recording_service->add_to_sales_session($this->merch_id, 1);
        $this->sales_recording_service->add_to_sales_session($merch_id2, 2);
        
        // Process sale recording
        $sale_data = array(
            'payment_type' => 'cash',
            'notes' => 'Multiple items'
        );
        
        $result = $this->sales_recording_service->process_sale_recording($sale_data);
        
        // Verify success
        $this->assertTrue($result['success']);
        
        // Verify both sales were recorded
        $sales_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}msp_sales");
        $this->assertEquals(2, $sales_count);
        
        // Verify stock was updated for both items
        $stock1 = get_post_meta($this->merch_id, '_msp_merchandise_stock', true);
        $stock2 = get_post_meta($merch_id2, '_msp_merchandise_stock', true);
        $this->assertEquals(99, $stock1); // 100 - 1
        $this->assertEquals(48, $stock2); // 50 - 2
    }
    
    public function test_insufficient_stock_validation() {
        // Set low stock
        update_post_meta($this->merch_id, '_msp_merchandise_stock', 5);
        
        // Try to add more than available stock
        $result = $this->sales_recording_service->add_to_sales_session($this->merch_id, 10);
        
        // Should return WP_Error
        $this->assertInstanceOf('WP_Error', $result);
        $this->assertEquals('insufficient_stock', $result->get_error_code());
    }
    
    public function test_update_sales_item_quantity() {
        // Add item to sales session
        $this->sales_recording_service->add_to_sales_session($this->merch_id, 1);
        
        // Update quantity
        $result = $this->sales_recording_service->update_sales_item($this->merch_id, 3);
        
        // Verify success
        $this->assertTrue($result);
        
        // Verify quantity was updated
        $sales_session = $this->sales_recording_service->get_sales_session();
        $this->assertEquals(3, $sales_session[$this->merch_id]['quantity']);
    }
    
    public function test_remove_from_sales_session() {
        // Add item to sales session
        $this->sales_recording_service->add_to_sales_session($this->merch_id, 1);
        
        // Remove item
        $result = $this->sales_recording_service->remove_from_sales_session($this->merch_id);
        
        // Verify success
        $this->assertTrue($result);
        
        // Verify item was removed
        $sales_session = $this->sales_recording_service->get_sales_session();
        $this->assertArrayNotHasKey($this->merch_id, $sales_session);
    }
    
    public function test_clear_sales_session() {
        // Add item to sales session
        $this->sales_recording_service->add_to_sales_session($this->merch_id, 1);
        
        // Clear sales session
        $result = $this->sales_recording_service->clear_sales_session();
        
        // Verify success
        $this->assertTrue($result);
        
        // Verify session is empty
        $sales_session = $this->sales_recording_service->get_sales_session();
        $this->assertEmpty($sales_session);
    }
    
    public function tearDown(): void {
        parent::tearDown();
        
        // Clean up test data
        MSP_Test_Utils::cleanup_test_data();
        
        // Clear sales session
        $this->sales_recording_service->clear_sales_session();
    }
}