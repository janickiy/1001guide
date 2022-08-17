import React from 'react';


const Tabs = ({links, current, setCurrent, extraClassName="nav-tabs"}) => {

  // make list of tabs
  const tabs = Object.keys(links).map((typeName, index) => {
    const handleClick = e => {
      e.preventDefault();
      setCurrent(typeName);
    };

    return (
      <li className="nav-item" key={index}>
        <a className={`nav-link ${current === typeName ? 'active' : null}`}
           href="#" onClick={handleClick}
        >
          {links[typeName]}
        </a>
      </li>
    );
  });

  // display
  return (
    <ul className={`nav mb-3 ${extraClassName}`}>
      {tabs}
    </ul>
  );

};


export default Tabs;