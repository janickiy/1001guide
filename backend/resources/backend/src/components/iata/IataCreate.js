import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';

const IataCreate = () => {

  return (
    <div className="create-page">
      <h1>Новый IATA</h1>
      <FormEdit fieldsToShow={fieldsToShow} itemType="iata" actionType="save" />
    </div>
  );

};

export default IataCreate;