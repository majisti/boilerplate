import * as React from 'react'
import * as ReactDOM from 'react-dom/'
import styled from 'styled-components'

const Title = styled.h1`
    color: red;
`;

ReactDOM.render(
    <Title>Hello, world!</Title>,
    document.getElementById('root')
);