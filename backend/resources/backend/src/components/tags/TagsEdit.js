import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';
import TagsTemplates from "./TagsTemplates";

const TagsEdit = ({match}) => {
  return (
    <div className="edit-page">
      <h1>Редактирование тега</h1>
      <FormEdit
        fieldsToShow={fieldsToShow} itemType="tags" itemId={Number(match.params.id)}
        multilang={true}
      />
      <TagsTemplates tagId={Number(match.params.id)} />
    </div>
  );

};

export default TagsEdit;