import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';

const LocationEdit = ({match}) => {
  return (
    <div className="edit-page">
      <h1>Редактирование локации</h1>
      <FormEdit
        fieldsToShow={fieldsToShow} itemType="locations" itemId={Number(match.params.id)}
        multilang={true} trackChanges={true}
      />
    </div>
  );

};

export default LocationEdit;