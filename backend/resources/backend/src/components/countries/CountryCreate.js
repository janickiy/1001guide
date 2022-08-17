import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';

const CountryCreate = () => {

  return (
    <div className="create-page">
      <h1>Новая страна</h1>
      <FormEdit
        fieldsToShow={fieldsToShow} itemType="countries" actionType="save"
        multilang={true} trackChanges={true}
      />
    </div>
  );

};

export default CountryCreate;