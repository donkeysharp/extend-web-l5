import React from 'react';
import { render } from 'react-dom';

import GridSearchContainer from './containers/GridSearchContainer';

document.addEventListener('DOMContentLoaded', () => {
  let el = document.getElementById('grid-root');
  render(<GridSearchContainer />, el);
});
