import { isValidUrl } from '../../src/util/UrlUtil'

/**
 * Test suite for the UrlUtil module.
 */
describe('UrlUtil', () => {
    /**
     * Test case to check if isValidUrl function returns true for a valid URL.
     */
    it('should return true for a valid URL', () => {
        const validUrls = [
            'https://www.example.com',
            'http://subdomain.example.com/page',
            'ftp://ftp.example.com/file',
            'http://localhost:3000',
            'file:///path/to/file.txt'
        ]

        validUrls.forEach(url => {
            expect(isValidUrl(url)).toBe(true)
        })
    })

    /**
     * Test case to check if isValidUrl function returns false for an invalid URL.
     */
    it('should return false for an invalid URL', () => {
        const invalidUrls = [
            'not a url',
            '12322123',
            null
        ]

        invalidUrls.forEach(url => {
            expect(isValidUrl(url)).toBe(false)
        })
    })
})
