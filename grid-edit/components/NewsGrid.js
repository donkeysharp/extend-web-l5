import React from 'react';
import ReactDataGrid from 'react-data-grid';

class NewsGrid extends React.Component {
  constructor(props) {
    super(props);

    this.createRows();
        this._columns = [
          { key: 'id', name: 'Nro', width: 40 },
          { key: 'date', name: 'Fecha', width: 90 },
          { key: 'client', name: 'Cliente', width: 100, editable: true },
          { key: 'eye', name: 'Ojo', width: 40, editable: true },
          { key: 'media', name: 'Medio', width: 100, editable: true },
          { key: 'title', name: 'Título Artículo', width: 300, editable: true },
          { key: 'pixels', name: 'Pixeles cm. col', editable: true },
          { key: 'equivalence', name: 'Equivalencia Publicitaria en Dólares', editable: true },
          { key: 'topic', name: 'Tema', width: 100, editable: true },
          { key: 'tendency', name: 'Tendencia', width: 90, editable: true },
          { key: 'type', name: 'Tipo', editable: true },
          { key: 'section', name: 'Sección', editable: true },
          { key: 'page', name: 'Página', editable: true },
          { key: 'code', name: 'Código', editable: true },
          { key: 'source', name: 'Fuente', editable: true },
          { key: 'alias', name: 'Alias', editable: true },
          { key: 'gender', name: 'Género', editable: true },
          { key: 'subtitle', name: 'Subtítulo', width: 300, editable: true } ];
  }
  createRows() {
    this._rows = [
      {
        id: 1,
        date: '20/05/2017',
        client: 'Yanbal',
        eye: 'B',
        media: 'El Mundo',
        title: 'su nueva fragancia',
        pixels: '',
        equivalence: '',
        topic: 'Belleza',
        tendency: 'Neutro',
        type: 'Impreso',
        page: '5',
        code: '',
        source: '',
        alias: '',
        gender: '',
        subtitle: 'MARCAS'
      }
    ];
  }
  rowGetter(i) {
    return this._rows[i];
  }
  render() {
    return <div>
      <ReactDataGrid
              columns={this._columns}
              enableCellSelect={true}
              rowGetter={this.rowGetter.bind(this)}
              rowsCount={this._rows.length}
              minHeight={500} />
    </div>;
  }
}

export default NewsGrid;
