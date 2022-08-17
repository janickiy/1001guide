import React from 'react';
import ItemList from '../ItemList';
import ButtonAdd from "../tables/ButtonAdd";

const Languages = () => {
  return (
    <div>
      <ButtonAdd />
      <ItemList
        tableData={["name", "code"]}
        columnWithLink={0}
        actions={["edit", "delete"]}
        type="languages"
      />
    </div>
  )
};

export default Languages;

