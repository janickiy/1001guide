import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';

const TemplateCreate = () => {

  return (
    <div className="create-page">
      <h1>Новая валюта</h1>
      <FormEdit fieldsToShow={fieldsToShow} itemType="templates" actionType="save" />
    </div>
  );

};

export default TemplateCreate;