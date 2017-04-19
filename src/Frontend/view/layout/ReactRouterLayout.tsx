import * as React from 'react'
import {Route} from 'react-router'

export interface ReactRouterLayoutProps {
    routes: Array<{
        path: string
        exact?: boolean
    }>
}

export abstract class ReactRouterLayout<P extends ReactRouterLayoutProps, S> extends React.Component<P, S> {
    public renderRoutes(section: string) {
        return this.props.routes.map((route, index) => (
            <Route
                key={index}
                path={route.path}
                exact={route.exact}
                component={route[section]}
            />
        ))
    }
}