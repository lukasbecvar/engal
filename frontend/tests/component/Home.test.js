import React from 'react';
import { render } from '@testing-library/react';
import '@testing-library/jest-dom';
global.React = React;

// import testing component
import Home from '../../src/component/Home';

test('renders Home component', () => {
    // create virtual dom
    const { getByText } = render(<Home/>);

    // get element with text
    const headingElement = getByText('Engal');

    // assert element
    expect(headingElement).toBeInTheDocument();
});
