import React from 'react';
import { render, fireEvent, waitFor } from '@testing-library/react';
import '@testing-library/jest-dom';

// mock the localStorage.setItem function
const localStorageMock = (() => {
    let store = {};
    return {
        getItem: jest.fn(),
        setItem: jest.fn((key, value) => {
            store[key] = value.toString();
        }),
        clear: jest.fn(() => {
            store = {};
        }),
    };
})();
Object.defineProperty(window, 'localStorage', {
    value: localStorageMock,
});

// mock window.location.reload
const reloadMock = jest.fn();
Object.defineProperty(window, 'location', {
    value: { reload: reloadMock },
});

// import testing component
import SetupComponent from '../../src/component/SetupComponent';

describe('SetupComponent', () => {
    it('renders Setup component', () => {
        const { getByText } = render(<SetupComponent />);
        const headingElement = getByText('Setup API URL');
        expect(headingElement).toBeInTheDocument();
    });

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
        expect(localStorage.setItem).not.toHaveBeenCalled();
    });

    it('handles form submission with valid URL', async () => {
        const { getByText, getByPlaceholderText } = render(<SetupComponent />);
        const input = getByPlaceholderText('http://localhost:1337');
        const submitButton = getByText('Submit');

        // mock fetch to return a fake response
        global.fetch = jest.fn().mockResolvedValue({
            ok: true,
            headers: { get: () => 'application/json' },
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
            expect(localStorage.setItem).toHaveBeenCalledWith('api-url', 'http://valid-url.com');
            expect(reloadMock).toHaveBeenCalled(); // check if window.location.reload() was called
        });
    });
});
