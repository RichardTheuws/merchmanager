/**
 * Example test file to verify testing setup
 */

describe('Example Test Suite', () => {
    test('basic arithmetic', () => {
        expect(1 + 1).toBe(2);
        expect(2 * 2).toBe(4);
    });

    test('string operations', () => {
        expect('hello').toContain('hell');
        expect('world').toHaveLength(5);
    });

    test('array operations', () => {
        const arr = [1, 2, 3];
        expect(arr).toHaveLength(3);
        expect(arr).toContain(2);
        expect(arr).not.toContain(4);
    });
});