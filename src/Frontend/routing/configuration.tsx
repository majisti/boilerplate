import * as React from 'react'
import Routes from 'routing/Routes'
import Homepage from 'view/section/home/index'
import Second from 'view/section/second/index'

export default [
    {
        path: Routes.HOME,
        exact: true,
        ...Homepage
    }, {
        path: Routes.SECOND,
        ...Second
    }
];