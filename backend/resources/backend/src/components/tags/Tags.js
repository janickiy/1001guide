import React from 'react';
import ItemList from '../ItemList';
import ButtonAdd from "../tables/ButtonAdd";

const Tags = () => {
  return (
    <div>
      <ButtonAdd />
      <ItemList
        tableData={["name"]}
        columnWithLink={0}
        actions={["edit", "delete"]}
        type="tags"
        multilang={true}
      />
    </div>
  )
};

export default Tags;

