import React from 'react';
import ItemList from '../ItemList';
import ButtonAdd from "../tables/ButtonAdd";

const Pages = () => {
  return (
    <div>
      <ButtonAdd />
      <ItemList
        tableData={["title", "code"]}
        columnWithLink={0}
        actions={["edit", "delete"]}
        type="pages"
        multilang={true}
      />
    </div>
  )
};

export default Pages;

