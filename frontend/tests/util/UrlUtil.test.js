/**
 * Test suite for the UrlUtil util.
 */
import { isValidUrl } from '../../src/util/UrlUtil';

describe('UrlUtil', () => {
    /**
     * Test case: should return true for a valid URL
     * 
     * Description:
     * - Verifies that the isValidUrl function returns true for valid URLs.
     * - Checks multiple valid URLs including HTTP, HTTPS, FTP, and file URLs.
     */
    it('should return true for a valid URL', () => {
        const validUrls = [
            'https://www.example.com',
            'http://subdomain.example.com/page',
            'ftp://ftp.example.com/file',
            'http://localhost:3000',
            'file:///path/to/file.txt'
        ];

        validUrls.forEach(url => {
            expect(isValidUrl(url)).toBe(true);
        });
    });

    /**
     * Test case: should return false for an invalid URL
     * 
     * Description:
     * - Verifies that the isValidUrl function returns false for invalid URLs.
     * - Checks with an invalid URL that is not recognized as a valid URL format.
     */
    it('should return false for an invalid URL', () => {
        const invalidUrls = [
            'not a url',
            '12322123',
            null
        ];

        invalidUrls.forEach(url => {
            expect(isValidUrl(url)).toBe(false);
        });
    });
});
