/**
 * Test suite for API utility functions.
 */
import { getApiStatus, isApiAvailable } from "../../src/util/ApiUtils";

describe('API Utility Functions', () => {
    /**
     * Test suite for the isApiAvailable function.
     */
    describe('isApiAvailable', () => {
        /**
         * Test case: should return true if API is available.
         */
        it('should return true if API is available', async () => {
            global.fetch = jest.fn().mockResolvedValue({ ok: true });
            const url = 'http://example.com/api';

            const result = await isApiAvailable(url);
            expect(result).toBeTruthy();
        });

        /**
         * Test case: should return false if API is not available.
         */
        it('should return false if API is not available', async () => {
            global.fetch = jest.fn().mockResolvedValue({ ok: false });
            const url = 'http://example.com/api';

            const result = await isApiAvailable(url);
            expect(result).toBeFalsy();
        });

        /**
         * Test case: should return false if there is an error.
         */
        it('should return false if there is an error', async () => {
            global.fetch = jest.fn().mockRejectedValue(new Error('Network Error'));
            const url = 'http://example.com/api';

            const result = await isApiAvailable(url);
            expect(result).toBeFalsy();
        });
    });

    /**
     * Test suite for the getApiStatus function.
     */
    describe('getApiStatus', () => {
        /**
         * Test case: should return status and message from API if request is successful.
         */
        it('should return status and message from API if request is successful', async () => {
            global.fetch = jest.fn().mockResolvedValue({
                json: jest.fn().mockResolvedValue({ status: 'success', message: 'API is working fine', backend_version: '1.0' })
            });
            const url = 'http://example.com/api';

            const result = await getApiStatus(url);
            expect(result).toEqual({ status: 'success', message: 'API is working fine', backend_version: '1.0' });
        });

        /**
         * Test case: should return error status if request is unsuccessful.
         */
        it('should return error status if request is unsuccessful', async () => {
            global.fetch = jest.fn().mockResolvedValue({
                json: jest.fn().mockResolvedValue({ status: 'error', message: 'API is down' })
            });
            const url = 'http://example.com/api';

            const result = await getApiStatus(url);
            expect(result).toEqual({ status: 'error', message: 'API is down', backend_version: null });
        });

        /**
         * Test case: should log an error if there is an error while fetching API status.
         */
        it('should log an error if there is an error while fetching API status', async () => {
            global.fetch = jest.fn().mockRejectedValue(new Error('Network Error'));
            const url = 'http://example.com/api';
            
            console.error = jest.fn();

            await getApiStatus(url);
            expect(console.error).toHaveBeenCalledWith('api connection error:', expect.any(Error));
        });
    });
});
