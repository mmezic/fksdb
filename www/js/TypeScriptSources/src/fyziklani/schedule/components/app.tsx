import * as React from 'react';
import {
    connect,
    Dispatch,
} from 'react-redux';
import { toggleChooser } from '../actions';
import { IFyziklaniScheduleStore } from '../reducers';
import { IData } from './index';
import Row from './row';

interface IProps {
    data: IData;
    description: string;
    label: string;
}

interface IState {
    showChooser?: boolean;

    onToggleChooser?(): void;
}

class Schedule extends React.Component<IProps & IState, {}> {

    public render() {
        const {data, showChooser, label, description, onToggleChooser} = this.props;
        const rows = [];
        for (const blockName in data) {
            if (data.hasOwnProperty(blockName)) {
                const blockData = data[blockName];
                rows.push(<Row key={blockName} blockData={blockData} blockName={blockName}/>);
            }
        }
// style={{display: showChooser ? '' : 'none'}}
        return (
            <div className={'bd-callout bd-callout-fyziklani'}>
                <h4>{label}</h4>
                <p className={'text-muted mb-3'} dangerouslySetInnerHTML={{__html: description}}/>
                {showChooser ?
                    (<div className={'schedule-field-container'}>
                        {rows}
                    </div>) :
                    (<div className={'text-center'}>
                        <button
                            className={'btn btn-fyziklani btn-block'}
                            onClick={(event) => {
                                event.preventDefault();
                                onToggleChooser();
                            }}
                        >...
                        </button>
                    </div>)
                }
            </div>
        );
    }
}

const mapStateToProps = (store: IFyziklaniScheduleStore): IState => {
    return {
        showChooser: store.compactValue.showChooser,
    };
};

const mapDispatchToProps = (dispatch: Dispatch<IFyziklaniScheduleStore>): IState => {
    return {
        onToggleChooser: () => dispatch(toggleChooser()),
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(Schedule);
