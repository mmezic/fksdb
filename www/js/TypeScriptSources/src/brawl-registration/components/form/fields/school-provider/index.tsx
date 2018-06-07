import * as React from 'react';
import { Async } from 'react-select';
import { WrappedFieldProps } from 'redux-form';
import { netteFetch } from '../../../../../fetch-api/middleware/fetch';
import { IInputProps } from '../../../../../person-provider/components/input-provider';
import ErrorDisplay from '../../../inputs/error-display';
import {
    IRequestData,
    ISchool,
} from './interfaces';

export default class SchoolProvider extends React.Component<IInputProps & WrappedFieldProps, {}> {

    public render() {
        const {
            input: {onChange, value},
            meta,
            JSXLabel,
            JSXDescription,
            input,
        } = this.props;
        const renderer = (v: ISchool) => {
            return (<span>
                        <img style={{height: '1em'}}
                             className="mr-2"
                             alt=""
                             src={'/flags/4x3/' + v.region.toLowerCase() + '.svg'}
                        />{v.label}</span>
            );
        };

        const loadOptions = (payload, cb) => {
            const netteJQuery: any = $;
            netteJQuery.nette.ext('unique', null);
            return netteFetch<IRequestData, ISchool[]>({
                act: 'school-provider',
                data: {
                    payload,
                },
            }, (response) => {
                cb(null, {
                    complete: true,
                    options: response.data,
                });
            }, (e) => {
                throw e;
            });
        };

        return <div className="form-group">
            <label>{JSXLabel}</label>
            {JSXDescription && (<small className="form-text text-muted">{JSXDescription}</small>)}
            <Async
                name="school-provider"
                value={value}
                onChange={onChange}
                loadOptions={loadOptions}
                optionRenderer={renderer}
                valueRenderer={renderer}
            />
            <ErrorDisplay input={input} meta={meta}/>
        </div>;
    }
}