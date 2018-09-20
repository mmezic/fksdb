import * as React from 'react';
import { Provider } from 'react-redux';
import {
    applyMiddleware,
    createStore,
} from 'redux';
import logger from 'redux-logger';
import { config } from '../../../config/';
import { INetteActions } from '../../../index';
import Downloader from '../../helpers/downloader/components/index';
import { app } from '../reducers';
import App from './app';

interface IProps {
    mode: string;
    actions: INetteActions;
}

export default class ResultsApp extends React.Component<IProps, {}> {
    public render() {
        const store = !config.dev ? createStore(app, applyMiddleware(logger)) : createStore(app);
        const accessKey = '@@fyziklani-results';
        const {mode, actions} = this.props;
        return (
            <Provider store={store}>
                <>
                    <Downloader accessKey={accessKey} actions={actions}/>
                    <App mode={mode} accessKey={accessKey}/>
                </>
            </Provider>
        );
    }
}
