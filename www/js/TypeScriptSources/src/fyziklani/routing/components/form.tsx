import * as React from 'react';
import {
    connect,
    Dispatch,
} from 'react-redux';
import { ITeam } from '../../helpers/interfaces';
import { saveTeams } from '../actions/save';
import { IFyziklaniRoutingStore } from '../reducers/';

interface IState {
    onSaveRouting?: (teams: ITeam[]) => void;
    teams?: ITeam[];
    saving?: boolean;
    error?: any;
}

class Form extends React.Component<IState, {}> {

    public render() {
        const {onSaveRouting, teams, saving, error} = this.props;
        return (<div>
            <button disabled={saving} className="btn btn-success" onClick={() => {
                onSaveRouting(teams);
            }}>Save
            </button>
            {error && (<span className="text-danger">{error.statusText}</span>)}
        </div>);
    }
}

const mapDispatchToProps = (dispatch: Dispatch<IFyziklaniRoutingStore>): IState => {
    return {
        onSaveRouting: (data: ITeam[]) => saveTeams(dispatch, data),
    };
};

const mapStateToProps = (state: IFyziklaniRoutingStore): IState => {
    return {
        error: state.fetchApi['accessKey'].error,
        saving: state.fetchApi['accessKey'].submitting,
        teams: state.teams.availableTeams,
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(Form);
