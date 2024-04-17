/**
 * Test suite for the SetupComponent.
 */
import React from 'react';
import { render, fireEvent, waitFor } from '@testing-library/react';
import { setApiUrlStorage } from '../../src/util/StorageUtil';
import '@testing-library/jest-dom';

// mock the setApiUrlStorage function
jest.mock('../../src/util/StorageUtil', () => ({
    setApiUrlStorage: jest.fn(),
}));

// import testing component
import SetupComponent from '../../src/component/SetupComponent';

describe('SetupComponent', () => {
    /**
     * Test case: Renders Setup component
     * 
     * Description:
     * - Verifies that the Setup component is rendered correctly.
     * - Checks if the heading "Setup API URL" is present in the component.
     */
    it('renders Setup component', () => {
        const { getByText } = render(<SetupComponent />);
        const headingElement = getByText('Setup API URL');
        expect(headingElement).toBeInTheDocument();
    });

    /**
     * Test case: Handles form submission with invalid URL
     * 
     * Description:
     * - Simulates form submission with an invalid URL.
     * - Waits for the error message "Invalid URL" to appear.
     * - Asserts that the API URL is not saved using the setApiUrlStorage function.
     */
    it('handles form submission with invalid URL', async () => {
        const { getByText, getByPlaceholderText } = render(<SetupComponent />);
        const input = getByPlaceholderText('http://localhost:1337');
        const submitButton = getByText('Submit');

        // simulate form submission with an invalid URL
        fireEvent.change(input, { target: { value: 'invalid-url' } });
        fireEvent.click(submitButton);

        // wait for error message to appear
        await waitFor(() => expect(getByText('Invalid URL')).toBeInTheDocument());

        // assert that API URL is not saved
        expect(setApiUrlStorage).not.toHaveBeenCalled();
    });

    /**
     * Test case: Handles form submission with valid URL
     * 
     * Description:
     * - Mocks the fetch function to return a fake response.
     * - Simulates form submission with a valid URL.
     * - Waits for the API call to be made and the response to be processed.
     * - Asserts that the API URL is saved using the setApiUrlStorage function.
     */
    it('handles form submission with valid URL', async () => {
        const { getByText, getByPlaceholderText } = render(<SetupComponent />);
        const input = getByPlaceholderText('http://localhost:1337');
        const submitButton = getByText('Submit');

        // mock fetch to return a fake response
        global.fetch = jest.fn().mockResolvedValue({
            ok: true,
            json: async () => ({
                status: 'success',
                code: 200,
                backend_version: '4.0',
                enabled_registration: true
            }),
        });

        // simulate form submission with a valid URL
        fireEvent.change(input, { target: { value: 'http://valid-url.com' } });
        fireEvent.click(submitButton);

        // wait for API response to be processed
        await waitFor(() => {
            expect(setApiUrlStorage).toHaveBeenCalledWith('http://valid-url.com');
        });
    });
});
 