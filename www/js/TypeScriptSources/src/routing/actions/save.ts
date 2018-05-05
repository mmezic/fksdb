import { Dispatch } from 'react-redux';
import { netteFetch } from '../../shared/helpers/fetch';
import { ITeam } from '../../shared/interfaces';
import { IStore } from '../reducers/index';

export const ACTION_SAVE_ROUTING_START = 'ACTION_SAVE_ROUTING_START';

export const ACTION_SAVE_ROUTING_SUCCESS = 'ACTION_SAVE_ROUTING_SUCCESS';
export const ACTION_SAVE_ROUTING_FAIL = 'ACTION_SAVE_ROUTING_FAIL';

export const ACTION_REMOVE_UPDATED_TEAMS = 'ACTION_REMOVE_UPDATED_TEAMS';

export const saveTeams = (dispatch: Dispatch<IStore>, teams: ITeam[]): Promise<any> => {
    dispatch(saveStart());
    return netteFetch({data: JSON.stringify(teams)},
        (e) => {
            dispatch(saveFail(e));
            throw e;
        },
        (d) => {
            dispatch(saveSuccess(d));
            setTimeout(() => {
                dispatch(removeUpdatesTeams());
            }, 5000);
        });
};

const saveStart = () => {
    return {
        type: ACTION_SAVE_ROUTING_START,
    };
};

const saveSuccess = (data) => {
    return {
        data,
        type: ACTION_SAVE_ROUTING_SUCCESS,
    };
};

const removeUpdatesTeams = () => {
    return {
        type: ACTION_REMOVE_UPDATED_TEAMS,
    };
};

const saveFail = (e) => {
    return {
        error: e,
        type: ACTION_SAVE_ROUTING_FAIL,
    };
};