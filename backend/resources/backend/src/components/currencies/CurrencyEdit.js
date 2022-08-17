import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';

const CurrencyEdit = ({match}) => {
  console.log(fieldsToShow);
  return (
    <div className="edit-page">
      <h1>Редактирование валюты</h1>
      <FormEdit fieldsToShow={fieldsToShow} itemType="currencies" itemId={Number(match.params.id)} />
    </div>
  );

};

export default CurrencyEdit;