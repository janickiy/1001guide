import React from 'react';

const VariableList = () => {

  const variables = {
    country: "Страна",
    country_in: "Страна с предлогом В",
    country_of: "Страна с предлогом кого/чего",
    city: "Город",
    city_in: "Город с предлогом В",
    city_of: "Город с предлогом кого/чего",
    tours: "Количество экскурсий",
    year: "Текущий год",
    // price: "Минимальная цена",
    poi: "Название достопримечательности",
    tag: "Название тега (для страницы тегов)"
  };

  const variablesToList = Object.keys(variables).map(
    (v, index) => <li key={index}>{`{${v}}`} &mdash; {variables[v]}</li>);

  return (
    <div className="alert alert-primary mb-3" role="alert">
      Список используемых переменных:<br/>
      <ul>{variablesToList}</ul>
    </div>
  );

};

export default VariableList;