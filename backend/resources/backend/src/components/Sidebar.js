import React from 'react';
import {NavLink} from 'react-router-dom';
import {adminUrl} from '../config';

const Sidebar = () => {

  const templateFieldsUrl = Number(localStorage.getItem('dev')) === 1 ?
    `${adminUrl}templates/` :
    `${adminUrl}templates-fields/`;

  return (
    <div className="col-sm-3">

      <ul className="nav flex-column">
        <li className="nav-item"><NavLink to={`${adminUrl}languages/`} className="nav-link">Языки</NavLink></li>
        <li className="nav-item"><NavLink to={`${adminUrl}currencies/`} className="nav-link">Валюты</NavLink></li>
        <li className="nav-item"><NavLink to={`${adminUrl}locations/`} className="nav-link">Города и страны</NavLink></li>
        <li className="nav-item"><NavLink to={`${adminUrl}poi/`} className="nav-link">Достопримечательности</NavLink></li>
        <li className="nav-item"><NavLink to={`${adminUrl}tags/`} className="nav-link">Теги</NavLink></li>
        <li className="nav-item"><NavLink to={`${adminUrl}settings/`} className="nav-link">Локальные настройки</NavLink></li>
        <li className="nav-item"><NavLink to={`${adminUrl}templates/`} className="nav-link">Шаблоны</NavLink></li>
        <li className="nav-item"><NavLink to={`${adminUrl}generate/`} className="nav-link">Генератор контента</NavLink></li>
        <li className="nav-item"><NavLink to={`${adminUrl}codes/`} className="nav-link">Встроенные коды</NavLink></li>
        <li className="nav-item"><NavLink to={`${adminUrl}export/`} className="nav-link">Экспорт</NavLink></li>
        <li className="nav-item"><NavLink to={`${adminUrl}import/`} className="nav-link">Импорт</NavLink></li>
      </ul>

    </div>
  );

};

export default Sidebar;
