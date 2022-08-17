import React, { useState, useEffect } from 'react';
import Loading from './Loading';
import ErrorBlock from './ErrorBlock';
import {sendRequest} from '../helpers/client-server';
import DataTable from './tables/DataTable';


const ItemList = ({
  tableData, columnWithLink, actions, type, groupBy=null,
  extraParams=null, multilang=false, showTitle=false,
  paginationEnabled=false, requestMethod='get'
}) => {

  // set default states
  const [requestError, setRequestError] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [listItems, setListItems] = useState({});
  const [language, setLanguage] = useState(
    window.localStorage.getItem("picked_lang") || "en"
  );
  const [pageTitle, setPageTitle] = useState(null);
  const [updated, forceUpdate] = useState();

  // pagination state
  const [offset, setOffset] = useState(0);
  const [paginationLoading, setPaginationLoading] = useState(false);
  const [isPageLast, setIsPageLast] = useState(false);


  // app DIV
  const appContainer = document.querySelector('.App');


  // get data from server
  useEffect(() => {
    loadData(true);
  }, [language, type, updated]);


  // load data on pagination
  useEffect(() => {
    if ( !offset || !paginationEnabled ) return;

    // set loader and prevent double run
    if ( paginationLoading ) return;
    setPaginationLoading(true);

    // bind scroll listener
    setTimeout(
      ()=>{
        if ( isPageLast ) return;
        setPaginationLoading(false);
        appContainer.addEventListener('scroll', handleScroll);
      },
      1500
    );

    // load new items
    loadData(false);

    // unbind scroll listener on Component close
    return () => {
      setOffset(0);
      appContainer.removeEventListener('scroll', handleScroll);
    };
  }, [offset]);


  // first run
  useEffect(() => {

    // scrolling pagination
    if ( paginationEnabled ) {
      appContainer.addEventListener('scroll', handleScroll);
      return () => {
        setOffset(0);
        appContainer.removeEventListener('scroll', handleScroll);
      };
    }

    if ( !showTitle ) return;
    sendRequest(type, 'get', Object.assign({
      action: "show_title",
    }, extraParams))
    .then(response => {
      setPageTitle(response.data.title);
    });
  }, []);


  /**
   * Load item list from the Server
   *
   * @param firstLoad
   */
  const loadData = firstLoad => {
    let dataToSend = {};
    if ( offset )
      dataToSend.offset = offset;
    let url = type;
    if ( extraParams )
      dataToSend = Object.assign( dataToSend, extraParams );
    if ( multilang ) {
      url += `?lang=${language}`;
      // dataToSend = Object.assign( dataToSend, {lang: language} );
    }

    sendRequest(url, requestMethod, dataToSend)
    .then(response => {

      console.log(response, dataToSend);

      setIsLoading(false);

      // on error
      if ( response.data.error ) {
        setRequestError(response.data.error);
        return false;
      }

      // if items did not received
      if ( !response.data.hasOwnProperty('items') ) {
        setRequestError("List items didn't received");
        return false;
      }

      let items = response.data.items;

      // sorting
      if ( groupBy ) {
        items = items.sort( (a,b) => {
          if ( a.fragment > b.fragment ) return 1;
          if ( a.fragment < b.fragment ) return -1;
          return 0;
        } );
      }

      // success
      setListItems(
        firstLoad ?
          items :
          [...listItems, ...items]
      );

      // mark last page
      if ( !items.length ) {
        setIsPageLast(true);
      }

      return true;

    })

    // request error
    .catch(error => {
      setRequestError(error.toString());
    });
  };


  /**
   * Load next page
   */
  const loadMore = () => {
    appContainer.removeEventListener('scroll', handleScroll);
    const offsetStep = 100;
    setOffset(offset + offsetStep);
  };


  /**
   * Scroll function
   */
  const handleScroll = () => {
    const loadWhenPxRemain = 700;
    const {scrollTop, scrollHeight} = appContainer;
    if ( (scrollHeight - scrollTop - window.innerHeight) < loadWhenPxRemain ) {
      loadMore();
    }
  };


  const pickLanguage = lang => {
    setLanguage(lang);
    window.localStorage.setItem("picked_lang", lang);
  };


  /**
   * "Load more" btn or Loader
   *
   * @return {*}
   * @constructor
   */
  const Paginator = () => {
    if ( !paginationEnabled || isLoading || isPageLast )
      return null;
    return paginationLoading ?
      <Loading /> :
      (
        <div className="text-center">
          <button className="btn btn-default" onClick={loadMore}>Показать ещё</button>
        </div>
      );
  };


  // output
  let content = null;
  if ( isLoading && !requestError ) {
    content = (
      <Loading/>
    );
  }
  else {
    content = (
      <DataTable
        tableData={tableData}
        columnWithLink={columnWithLink}
        actions={actions}
        listItems={listItems}
        setError={setRequestError}
        setIsLoading={setIsLoading}
        setListItems={setListItems}
        type={type}
      />
    );
  }

  return (
    <div>
      {(requestError) ? (<ErrorBlock>{requestError}</ErrorBlock>) : null}
      {pageTitle ?
        (<h1>{pageTitle}</h1>) : null
      }
      {content}
      <Paginator/>
    </div>
  );
};

export default ItemList;