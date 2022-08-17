import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';

const IataEdit = ({match}) => {
  return (
    <div className="edit-page">
      <h1>Редактирование IATA</h1>
      <FormEdit fieldsToShow={fieldsToShow} itemType="iata" itemId={Number(match.params.id)} />
    </div>
  );

};

export default IataEdit;