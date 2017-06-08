import React from 'react';
import { render } from 'react-dom';

import Foo from './components/Foo';

document.addEventListener('DOMContentLoaded', () => {
  let el = document.getElementById('grid-root');
  render(<Foo />, el);
});
