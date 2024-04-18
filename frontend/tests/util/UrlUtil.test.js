import { isValidUrl } from '../../src/util/UrlUtil';

describe('UrlUtil', () => {

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
