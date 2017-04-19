import * as React from 'react'
import {Link} from 'react-router-dom'
import Routes from 'routing/Routes'
import {ReactRouterLayout, ReactRouterLayoutProps} from 'view/layout/ReactRouterLayout'

export interface TwoRowsLayoutProps extends ReactRouterLayoutProps {
    routes: Array<{
        path: string
        exact?: boolean
        header: any
        helmet: any
        main: any
    }>
}

export default class TwoRowsLayout extends ReactRouterLayout<TwoRowsLayoutProps, null> {
    public render() {
        return (
            <div>
                {this.renderRoutes('helmet')}
                <div className='header'>
                    <Link to={Routes.HOME}>Home</Link>
                    <Link to={Routes.SECOND}>Second</Link>
                    {this.renderRoutes('header')}
                </div>
                <div className='content'>
                    {this.renderRoutes('main')}
                </div>
            </div>
        );
    }
}