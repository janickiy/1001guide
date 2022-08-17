import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';

const CodeEdit = ({match}) => {
  console.log(fieldsToShow);
  return (
    <div className="edit-page">
      <h1>Редактирование кода</h1>
      <FormEdit fieldsToShow={fieldsToShow} itemType="codes" itemId={Number(match.params.id)} />
    </div>
  );

};

export default CodeEdit;