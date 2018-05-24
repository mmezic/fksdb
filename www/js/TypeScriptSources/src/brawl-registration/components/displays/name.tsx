import * as React from 'react';
import { connect } from 'react-redux';
import {
    getParticipantValues,
    IPersonSelector,
} from '../../middleware/price';
import { FORM_NAME } from '../form';

interface IState {
    familyName?: string;
    otherName?: string;
}

class NameDisplay extends React.Component<IPersonSelector & IState, {}> {
    public render() {
        const {index, type, familyName, otherName} = this.props;
        return <>
            {(otherName && familyName) ?
                (<span>{otherName} {familyName}</span>) :
                (<span>{index + 1} {type}</span>)}
        </>;
    }
}

const mapDispatchToProps = () => {
    return {};
};

const mapStateToProps = (state, ownProps: IPersonSelector): IState => {
    const values = getParticipantValues(FORM_NAME, state, ownProps);
    return {
        familyName: values.familyName,
        otherName: values.otherName,
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(NameDisplay);
