import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';

const LocationCreate = () => {

  return (
    <div className="create-page">
      <h1>Новая локация</h1>
      <FormEdit
        fieldsToShow={fieldsToShow} itemType="locations" actionType="save"
        multilang={true} trackChanges={true}
      />
    </div>
  );

};

export default LocationCreate;