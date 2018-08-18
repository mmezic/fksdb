import * as React from 'react';
import {
    connect,
    Dispatch,
} from 'react-redux';
import Card from '../../../shared/components/card';
import Powered from '../../../shared/powered';
import {
    IRoom,
    ITeam,
} from '../../helpers/interfaces';
import { addTeams } from '../actions/teams';
import { IFyziklaniRoutingStore } from '../reducers/';
import Form from './form/index';
import Rooms from './rooms/index';
import UnRoutedTeams from './unrouted-teams/';

interface IState {
    onAddTeams?: (teams: ITeam[]) => void;
}

interface IProps {
    teams: ITeam[];
    rooms: IRoom[];
}

class RoutingApp extends React.Component<IState & IProps, {}> {

    public componentDidMount() {
        const {onAddTeams, teams} = this.props;
        onAddTeams(teams);
    }

    public render() {
        const {rooms} = this.props;

        return (
            <div>
                <Card headline={null} level="secondary">
                    <div className="row">
                        <div className="col-lg-8" style={{overflowY: 'scroll', maxHeight: '700px'}}>
                            <Rooms rooms={rooms}/>
                        </div>
                        <div className="col-lg-4" style={{overflowY: 'scroll', maxHeight: '700px'}}>
                            <UnRoutedTeams/>
                        </div>
                    </div>
                </Card>
                <div>
                    <Form accessKey={'@@fyziklani/routing'}/>
                </div>
                <Powered/>
            </div>
        );
    }
}

const mapStateToProps = (): IState => {
    return {};
};

const mapDispatchToProps = (dispatch: Dispatch<IFyziklaniRoutingStore>): IState => {
    return {
        onAddTeams: (teams) => dispatch(addTeams(teams)),
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(RoutingApp);
