import * as React from 'react'
import {createStore, combineReducers, applyMiddleware} from 'redux'
import {Provider} from 'react-redux'
import createHistory from 'history/createBrowserHistory'
import {ConnectedRouter, routerReducer, routerMiddleware} from 'react-router-redux'
import TwoRowsLayout from 'view/layout/TwoRowsLayout'
import configuration from 'routing/configuration'

export default class App extends React.Component<null, null> {
    private history;
    private store;

    public componentWillMount() {
        this.history = createHistory()
        const middleware = routerMiddleware(this.history)
        this.store = createStore(
            combineReducers({
                router: routerReducer
            }),
            applyMiddleware(middleware)
        )
    }

    public render() {
        return (
            <Provider store={this.store}>
                <ConnectedRouter history={this.history}>
                    <TwoRowsLayout routes={configuration} />
                </ConnectedRouter>
            </Provider>
        )
    }
}