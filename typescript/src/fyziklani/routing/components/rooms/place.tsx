import * as React from 'react';
import { connect } from 'react-redux';
import {
    Action,
    Dispatch,
} from 'redux';
import {
    Place,
    Team,
} from '../../../helpers/interfaces/';
import { dropItem } from '../../actions/dragndrop';
import { DragNDropData } from '../../middleware/interfaces';
import { Store as RoutingStore } from '../../reducers/';
import TeamComponent from '../team/';

interface State {
    onDrop?: (teamId: number, place: Place) => void;
    teams?: Team[];
    draggedTeamId?: number;
}

interface Props {
    x: number;
    y: number;
    roomId: number;
}

class PlaceComponent extends React.Component<State & Props, {}> {
    public render() {
        const {x, y, onDrop, teams, draggedTeamId, roomId} = this.props;
        const team = teams && teams.filter((currentTeam) => {
            return (currentTeam.x === x) && (currentTeam.y === y) && (currentTeam.roomId === roomId);
        })[0];
        return (<td
            onDragOver={(e) => {
                if (!team) {
                    e.preventDefault();
                }
            }}
            onClick={() => {
                if (team) {
                    return null;
                }
                return draggedTeamId ? onDrop(draggedTeamId, {x, y, roomId, room: null}) : null;
            }}
            onDrop={() => {
                onDrop(draggedTeamId, {x, y, roomId, room: null});
            }}>
            {team && <TeamComponent
                team={team}
            />}
        </td>);
    }
}

const mapDispatchToProps = (dispatch: Dispatch<Action<string>>): State => {
    return {
        onDrop: (teamId, place) => dispatch(dropItem<DragNDropData>({teamId, place})),
    };
};

const mapStateToProps = (state: RoutingStore): State => {
    return {
        draggedTeamId: (state.dragNDrop.data && state.dragNDrop.data.hasOwnProperty('teamId')) ? state.dragNDrop.data.teamId : null,
        teams: state.teams.availableTeams,
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(PlaceComponent);
