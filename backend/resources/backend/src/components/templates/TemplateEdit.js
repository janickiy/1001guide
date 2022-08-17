import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';

const TemplateEdit = ({match}) => {
  console.log(fieldsToShow);
  return (
    <div className="edit-page">
      <h1>Редактирование валюты</h1>
      <FormEdit fieldsToShow={fieldsToShow} itemType="templates" itemId={Number(match.params.id)} />
    </div>
  );

};

export default TemplateEdit;