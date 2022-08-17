import {siteUrl} from './client-server';

/**
 * Generata list of action links
 *
 * @param {array} actions. [edit, delete, show, showFrontend]
 * @param {int} id
 * @return {object}
 */
const generateActionLinks = (actions, id, item=null) => {
  const currentUrl = '';
  let links = {
    edit: currentUrl + id + '/edit/'
  };

  if ( actions.includes("show") )
    links.show = currentUrl + id + '/';

  if ( actions.includes("view") )
    links.view = `${siteUrl}/${item.type}/${item.slug}.htm`;

  return links;
};

export {generateActionLinks};