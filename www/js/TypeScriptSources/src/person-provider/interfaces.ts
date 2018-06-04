import { IState as ISubmitStore } from '../fetch-api/reducers/submit';
import {
    IProviderStore,
} from './reducers/provider';

export interface IRequestData {
    login: string;
    password: string;
    fields: string[];
}

export interface IStore {
    submit: ISubmitStore;
    provider: IProviderStore;
}

export interface IProviderValue<D = any> {
    hasValue: boolean;
    value: D;
}

export interface IResponseData {
    key?: string;
    fields: {
        [value: string]: IProviderValue<any>;
    };
}
