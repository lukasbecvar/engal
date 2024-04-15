import { getApiUrl, setApiUrlStorage, deleteApiUrlFormStorage } from '../../src/util/StorageUtil';

describe('StorageUtil', () => {
    beforeEach(() => {
        // reset localStorage before each test
        localStorage.clear();
    });

    it('should retrieve API URL from localStorage', () => {
        // set API URL in localStorage
        const apiUrl = 'https://api.example.com';
        localStorage.setItem('api-url', apiUrl);

        // retrieve API URL
        const retrievedUrl = getApiUrl();

        // check if retrieved URL matches the one set
        expect(retrievedUrl).toEqual(apiUrl);
    });

    it('should set API URL in localStorage', () => {
        // Define API URL
        const apiUrl = 'https://api.example.com';
    
        // Mock window.location.reload() function
        const reloadMock = jest.fn();
        delete window.location;
        window.location = { reload: reloadMock };
    
        // Set API URL using the utility function
        setApiUrlStorage(apiUrl);
    
        // Retrieve API URL from localStorage
        const retrievedUrl = localStorage.getItem('api-url');
    
        // Check if retrieved URL matches the one set
        expect(retrievedUrl).toEqual(apiUrl);
    
        // Check if window.location.reload() is called
        expect(reloadMock).toHaveBeenCalled();
    });
    

    it('should delete API URL from localStorage', () => {
        // set API URL in localStorage
        const apiUrl = 'https://api.example.com';
        localStorage.setItem('api-url', apiUrl);

        // mock window.location.reload() function
        const reloadMock = jest.fn();
        delete window.location;
        window.location = { reload: reloadMock };

        // call the function to delete API URL
        deleteApiUrlFormStorage();

        // check if API URL is deleted from localStorage
        const retrievedUrl = localStorage.getItem('api-url');
        expect(retrievedUrl).toBeNull();
    });
});
