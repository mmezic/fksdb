import * as React from 'react';
import { connect } from 'react-redux';
import {
    IStore,
} from '../interfaces';
import Form from './form';
import { getAccessKey } from '../validation';

interface IProps {
    accessKey: string;
    html?: string;
}

interface IState {
    personId?: { hasValue: boolean; value: string };
}

class PersonProvider extends React.Component<IProps & IState, {}> {

    public render() {
        const {children} = this.props;

        if (this.props.personId) {
            if (children) {
                return <div>
                    {children}
                </div>;
            } else {
                return <div dangerouslySetInnerHTML={{__html: this.props.html}}/>;
            }

        } else {
            return <Form accessKey={this.props.accessKey}/>;
        }
    }
}

const mapDispatchToProps = (): IState => {
    return {};
};

const mapStateToProps = (state: IStore, ownProps: IProps): IState => {
    const accessKey = ownProps.accessKey;
    if (state.provider.hasOwnProperty(accessKey)) {
        return {
            personId: state.provider[accessKey].fields[getAccessKey(accessKey, 'person.personId')],
        };
    }
    return {};
};

export default connect(mapStateToProps, mapDispatchToProps)(PersonProvider);