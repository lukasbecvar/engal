import { getApiStatus, isApiAvailable } from "../../src/util/ApiUtils";

global.console.log = jest.fn();

describe('isApiAvailable function', () => {
    test('should return true when API is available', async () => {
        // Mock the fetch function to simulate a successful response
        global.fetch = jest.fn().mockResolvedValueOnce({ ok: true });

        const url = 'http://example.com/api';
        const result = await isApiAvailable(url);

        expect(result).toBe(true);
    });

    test('should return false when API is not available', async () => {
        // Mock the fetch function to simulate a failed response
        global.fetch = jest.fn().mockResolvedValueOnce({ ok: false });

        const url = 'http://example.com/api';
        const result = await isApiAvailable(url);

        expect(result).toBe(false);
    });

    test('should return false when fetch throws an error', async () => {
        // Mock the fetch function to simulate an error
        global.fetch = jest.fn().mockRejectedValueOnce(new Error('Network error'));

        const url = 'http://example.com/api';
        const result = await isApiAvailable(url);

        expect(result).toBe(false);
    });
});

describe('getApiStatus function', () => {
    test('should return the correct status and data when API returns success', async () => {
        const mockData = {
            status: 'success',
            message: 'API is up and running',
            backend_version: '1.0.0'
        };

        global.fetch = jest.fn().mockResolvedValueOnce({
            ok: true,
            headers: { get: () => 'application/json' },
            json: () => Promise.resolve(mockData)
        });

        const url = 'http://example.com/api';
        const result = await getApiStatus(url);

        expect(result).toEqual({
            status: 'success',
            message: 'API is up and running',
            backend_version: '1.0.0'
        });
    });

    test('should return error status and message when API returns error', async () => {
        const mockData = {
            status: 'error',
            message: 'API is down'
        };

        global.fetch = jest.fn().mockResolvedValueOnce({
            ok: true,
            headers: { get: () => 'application/json' },
            json: () => Promise.resolve(mockData)
        });

        const url = 'http://example.com/api';
        const result = await getApiStatus(url);

        expect(result).toEqual({
            status: 'error',
            message: 'API is down',
            backend_version: null
        });
    });

    test('should return error status and message when API response is not JSON', async () => {
        global.fetch = jest.fn().mockResolvedValueOnce({
            ok: true,
            headers: { get: () => 'text/html' }
        });

        const url = 'http://example.com/api';
        const result = await getApiStatus(url);

        expect(result).toEqual({
            status: 'error',
            message: 'Unknown error',
            backend_version: null
        });
    });

    test('should return error status and message when fetch throws an error', async () => {
        global.fetch = jest.fn().mockRejectedValueOnce(new Error('Network error'));

        const url = 'http://example.com/api';
        const result = await getApiStatus(url);

        expect(result).toEqual({
            status: 'error',
            message: 'Unknown error',
            backend_version: null
        });
    });
});
