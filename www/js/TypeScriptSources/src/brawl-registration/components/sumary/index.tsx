import * as React from 'react';
import { connect } from 'react-redux';
import {
    IAccommodationItem,
    IPersonDefinition,
    IScheduleItem,
} from '../../middleware/iterfaces';
import {
    getAccommodationFromState,
    getAccommodationPrice,
    getParticipantValues,
    getScheduleFromState,
    getSchedulePrice,
} from '../../middleware/price';
import { IStore } from '../../reducers';
import NameDisplay from '../displays/name';
import PriceDisplay from '../displays/price';
import { FORM_NAME } from '../form';

interface IState {
    data?: Array<{
        acc: any;
        selector: IPersonDefinition;
        schedule: any;
        name: {
            familyName: string;
            otherName: string;
        };
    }>;
    accommodationDef?: IAccommodationItem[];
    scheduleDef?: IScheduleItem[];
}

class Summary extends React.Component<IState, {}> {

    public render() {
        const rows = [];
        const {accommodationDef, scheduleDef} = this.props;
        const accSum = {kc: 0, eur: 0};
        const scheduleSum = {kc: 0, eur: 0};

        this.props.data.forEach((personData, index) => {
            const accommodationPrice = getAccommodationPrice(accommodationDef, personData.acc);
            const schedulePrice = getSchedulePrice(scheduleDef, personData.schedule);
            accSum.kc += accommodationPrice.kc;
            accSum.eur += accommodationPrice.eur;

            scheduleSum.kc += schedulePrice.kc;
            scheduleSum.eur += schedulePrice.eur;

            rows.push(<tr key={index}>
                <td>
                    <NameDisplay type={personData.selector.type} index={personData.selector.index}/>
                </td>
                <td>
                    <PriceDisplay eur={accommodationPrice.eur} kc={accommodationPrice.kc}/>
                </td>
                <td>
                    <PriceDisplay eur={schedulePrice.eur} kc={schedulePrice.kc}/>
                </td>
                <td>
                    <PriceDisplay eur={schedulePrice.eur + accommodationPrice.eur} kc={schedulePrice.kc + accommodationPrice.kc}/>
                </td>
            </tr>);
        });

        rows.push(<tr key={'sum'} className="table-primary">
            <td>
                sum
            </td>
            <td>
                <PriceDisplay eur={accSum.eur} kc={accSum.kc}/>
            </td>
            <td>
                <PriceDisplay eur={scheduleSum.eur} kc={scheduleSum.kc}/>
            </td>
            <td>
                <PriceDisplay eur={scheduleSum.eur + accSum.eur} kc={scheduleSum.kc + accSum.kc}/>
            </td>
        </tr>);
        return <div>
            <table className="table table-striped">
                <thead>
                <tr>
                    <th>name</th>
                    <th>Accommodation price</th>
                    <th>Schedule price</th>
                    <th>total</th>
                </tr>
                </thead>
                <tbody>
                {rows}
                </tbody>
            </table>
        </div>;
    }
}

const mapDispatchToProps = (): IState => {
    return {};
};

const mapStateToProps = (state: IStore): IState => {
    const data = [];
    state.definitions.persons.forEach((person) => {
        const formValues = getParticipantValues(FORM_NAME, state, {index: person.index, type: person.type});
        data.push({
            ...getAccommodationFromState(FORM_NAME, state, {index: person.index, type: person.type}),
            name: {
                familyName: formValues.familyName,
                otherName: formValues.otherName,
            },
            ...getScheduleFromState(FORM_NAME, state, {index: person.index, type: person.type}),
            selector: {index: person.index, type: person.type},
        });
    });

    return {
        accommodationDef: state.definitions.accommodation,
        data,
        scheduleDef: state.definitions.schedule,
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(Summary);
