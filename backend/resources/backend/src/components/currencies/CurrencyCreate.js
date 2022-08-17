import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';

const CurrencyCreate = () => {

  return (
    <div className="create-page">
      <h1>Новая валюта</h1>
      <FormEdit fieldsToShow={fieldsToShow} itemType="currencies" actionType="save" />
    </div>
  );

};

export default CurrencyCreate;