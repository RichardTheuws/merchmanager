<?php

use PHPUnit\Framework\TestCase;

class TestDatabase extends TestCase {
    
    private $database;
    
    public function setUp(): void {
        parent::setUp();
        $this->database = new Merchmanager_Database();
    }
    
    public function test_tables_creation() {
        global $wpdb;
        
        // Ensure tables don't exist initially
        $this->assertFalse( $this->database->tables_exist() );
        
        // Create tables
        $this->database->create_tables();
        
        // Verify tables exist
        $this->assertTrue( $this->database->tables_exist() );
        
        // Verify table structure
        $tables = array( 'msp_sales', 'msp_stock_log', 'msp_stock_alerts' );
        
        foreach ( $tables as $table ) {
            $table_name = $wpdb->prefix . $table;
            $result = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" );
            $this->assertEquals( $table_name, $result );
        }
    }
    
    public function test_sample_data_insertion() {
        global $wpdb;
        
        // Create test data first
        $band_id = MSP_Test_Utils::create_test_band();
        $merch_id = MSP_Test_Utils::create_test_merchandise( $band_id );
        $tour_id = MSP_Test_Utils::create_test_tour( $band_id );
        $show_id = MSP_Test_Utils::create_test_show( $tour_id );
        
        // Insert sample data
        $this->database->insert_sample_data();
        
        // Verify sales were inserted
        $sales_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}msp_sales" );
        $this->assertGreaterThan( 0, $sales_count );
        
        // Verify stock logs were created
        $stock_logs_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}msp_stock_log" );
        $this->assertGreaterThan( 0, $stock_logs_count );
        
        // Clean up
        MSP_Test_Utils::cleanup_test_data();
    }
    
    public function tearDown(): void {
        parent::tearDown();
        MSP_Test_Utils::cleanup_test_data();
    }
}