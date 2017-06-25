import React from 'react';
import ReactDataGrid from 'react-data-grid';
import {Editors, Formatters} from 'react-data-grid-addons';

const { DropDownEditor } = Editors;
const { DropDownFormatter } = Formatters;

function getComponents(items) {
  let componentTypes = [];
  items.forEach((item) => {
    componentTypes.push({
      id: item.id + '',
      value: item.id + '',
      text: item.name,
      title: item.name
    });
  })
  return {
    DropDownEditor: <DropDownEditor options={componentTypes} />,
    DropDownFormatter: <DropDownFormatter options={componentTypes} value={items[0].id+''} />
  }
}

function getClassificationComponents() {
  return getComponents([
    { id: 'A', name: 'A' },
    { id: 'B', name: 'B' },
    { id: 'C', name: 'C' }
  ]);
}

function getTendencyComponents() {
  return getComponents([
    { id: '1', name: 'Positivo' },
    { id: '2', name: 'Negativo' },
    { id: '3', name: 'Neutro' }
  ]);
}

function getTypeComponents() {
  return getComponents([
    { id: '1', name: 'Impreso' },
    { id: '2', name: 'Digital' },
    { id: '3', name: 'Radio' },
    { id: '4', name: 'TV' }
  ]);
}

class NewsGrid extends React.Component {
  constructor(props) {
    super(props);

    const {
      DropDownEditor: clientDDE,
      DropDownFormatter: clientDDF
    } = getComponents(this.props.clients);

    const {
      DropDownEditor: mediaDDE,
      DropDownFormatter: mediaDDF
    } = getComponents(this.props.media)

    const {
      DropDownEditor: topicDDE,
      DropDownFormatter: topicDDF
    } = getComponents(this.props.topics);

    const {
      DropDownEditor: classificationDDE,
      DropDownFormatter: classificationDDF
    } = getClassificationComponents();

    const {
      DropDownEditor: tendencyDDE,
      DropDownFormatter: tendencyDDF
    } = getTendencyComponents();

    const {
      DropDownEditor: typeDDE,
      DropDownFormatter: typeDDF
    } = getTypeComponents();

    this.createNewsRows();
    this._columns = [
      { key: 'index', name: 'Nro', width: 40 },
      { key: 'date', name: 'Fecha', width: 90 },
      {
        key: 'client',
        name: 'Cliente',
        width: 100,
        editable: true,
        editor: clientDDE,
        formatter: clientDDF
      },
      {
        key: 'eye',
        name: 'Ojo',
        width: 60,
        editable: true,
        editor: classificationDDE,
        formatter: classificationDDF
      },
      {
        key: 'media',
        name: 'Medio',
        width: 100,
        editable: true,
        editor: mediaDDE,
        formatter: mediaDDF
      },
      { key: 'title', name: 'Título Artículo', width: 300, editable: true },
      { key: 'pixels', name: 'Pixeles cm. col', editable: true },
      { key: 'equivalence', name: 'Equivalencia Publicitaria en Dólares', editable: true },
      {
        key: 'topic',
        name: 'Tema',
        width: 100,
        editable: true,
        editor: topicDDE,
        formatter: topicDDF
      },
      {
        key: 'tendency',
        name: 'Tendencia',
        width: 90,
        editable: true,
        editor: tendencyDDE,
        formatter: tendencyDDF
      },
      {
        key: 'type',
        name: 'Tipo',
        editable: true,
        editor: typeDDE,
        formatter: typeDDF
      },
      { key: 'section', name: 'Sección', editable: true },
      { key: 'page', name: 'Página', editable: true },
      { key: 'code', name: 'Código', editable: true },
      { key: 'source', name: 'Fuente', editable: true },
      { key: 'alias', name: 'Alias', editable: true },
      { key: 'gender', name: 'Género', editable: true },
      { key: 'subtitle', name: 'Subtítulo', width: 300, editable: true }
    ];
  }
  createNewsRows() {
    let rows = [];
    this.props.news.forEach((item, idx) => {

      rows.push({
        index: idx + 1,
        date: item.news ? item.news.date: '',
        client: item.news.client ? item.news.client.name : '',
        eye: item.news ? item.news.clasification : '',
        media: item.media ? item.media.name : '',
        title: item.title ? item.title : '',
        pixels: item.measure ? item.measure : '',
        equivalence: item.cost ? item.cost : '',
        topic: item.topic ? item.topic.name : '',
        tendency: item.tendency ? item.tendency : '',
        type: item.type ? item.type : '',
        section: item.section ? item.section : '',
        page: item.page ? item.page : '',
        code: item.code ? item.code : '',
        source: item.source ? item.source : '',
        alias: item.alias ? item.alias : '',
        gender: item.gender ? item.gender : '',
        subtitle: item.subtitle ? item.subtitle : '',
      })
    });
    this._rows = rows;
  }
  rowGetter(i) {
    return this._rows[i];
  }
  handleGridRowsUpdated({ fromRow, toRow, updated }) {
    console.log(fromRow, toRow, updated)
    this._rows[fromRow] = Object.assign({}, this._rows[fromRow], updated);
  }
  handleSaveClick() {
    console.log(this._rows)
  }
  render() {
    return <div>
      <div className="row">
        <div className="col-md-1 col-md-offset-11">
          <button
            className="btn btn-primary"
            onClick={this.handleSaveClick.bind(this)}>
            <i className="fa fa-save"></i>
          </button>
        </div>
      </div>
      <ReactDataGrid
        columns={this._columns}
        cellNavigationMode={'changeRow'}
        enableCellSelect={true}
        rowGetter={this.rowGetter.bind(this)}
        rowsCount={this._rows.length}
        minHeight={1500}
        onGridRowsUpdated={this.handleGridRowsUpdated.bind(this)} />
    </div>;
  }
}

export default NewsGrid;
