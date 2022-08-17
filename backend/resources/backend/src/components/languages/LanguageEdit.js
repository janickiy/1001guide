import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';

const LanguageEdit = ({match}) => {
  console.log(fieldsToShow);
  return (
    <div className="edit-page">
      <h1>Редактирование языка</h1>
      <FormEdit fieldsToShow={fieldsToShow} itemType="languages" itemId={Number(match.params.id)} />
    </div>
  );

};

export default LanguageEdit;