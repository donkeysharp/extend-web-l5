import React from 'react';

class NewsGrid extends React.Component {
  render() {
    return <h1>{this.props.news.length}</h1>
  }
}

export default NewsGrid;
