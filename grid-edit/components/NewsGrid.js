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
    DropDownEditor: <DropDownEditor
      options={componentTypes}
    />,
    DropDownFormatter: <DropDownFormatter
      options={componentTypes} value={items[0].id+''}
    />
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

    // this.createNewsRows();
    this._columns = [
      { key: 'index', name: 'Nro', width: 40 },
      { key: 'date', name: 'Fecha', width: 90 },
      {
        key: 'client_id',
        name: 'Cliente',
        width: 100,
        editable: true,
        editor: clientDDE,
        formatter: clientDDF
      },
      {
        key: 'clasification',
        name: 'Ojo',
        width: 60,
        editable: true,
        editor: classificationDDE,
        formatter: classificationDDF
      },
      {
        key: 'media_id',
        name: 'Medio',
        width: 100,
        editable: true,
        editor: mediaDDE,
        formatter: mediaDDF
      },
      { key: 'title', name: 'Título Artículo', width: 300, editable: true },
      { key: 'measure', name: 'Pixeles cm. col', editable: true },
      { key: 'cost', name: 'Equivalencia Publicitaria en Dólares', editable: true },
      {
        key: 'topic_id',
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
  parseRow(index) {

    let item = this.props.news[index];
    return {
      index: index + 1,
      date: item.news ? item.news.date: '',
      client_id: item.client_id ? item.client_id : '',
      clasification: item.news ? item.news.clasification : '',
      media_id: item.media_id ? item.media_id : '',
      title: item.title ? item.title : '',
      measure: item.measure ? item.measure : '',
      cost: item.cost ? item.cost : '',
      topic_id: item.topic_id ? item.topic_id : '',
      tendency: item.tendency ? item.tendency : '',
      type: item.type ? item.type : '',
      section: item.section ? item.section : '',
      page: item.page ? item.page : '',
      code: item.news.code ? item.news.code : '',
      source: item.source ? item.source : '',
      alias: item.alias ? item.alias : '',
      gender: item.gender ? item.gender : '',
      subtitle: item.subtitle ? item.subtitle : '',
    };
  }
  handleGridRowsUpdated({ fromRow, toRow, updated }) {
    if (this.props.onCellChange) {
      this.props.onCellChange(fromRow, updated);
    }
  }
  handleSaveClick() {
    if (this.props.onSaveClick) {
      this.props.onSaveClick();
    }
  }
  render() {
    return <div>
      <div className="row">
        <div className="col-md-1">
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
        rowGetter={this.parseRow.bind(this)}
        rowsCount={this.props.news.length}
        minHeight={500}
        onGridRowsUpdated={this.handleGridRowsUpdated.bind(this)} />
    </div>;
  }
}

export default NewsGrid;
