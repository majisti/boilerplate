import * as React from 'react'

export default class SimpleLayout extends React.Component<null, null> {
    public render() {
        return this.props.children as JSX.Element;
    }
}