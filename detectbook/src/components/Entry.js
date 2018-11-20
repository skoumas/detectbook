import React, { Component } from 'react';
import User from './User';
import './Entry.css';

class Entry extends React.Component {

	constructor(props) {
		super();
	}

	componentDidMount(props) {
	}

	render() {
		return (


			<div className="entry">

				<div class="card">

				  <div class="card-body">
				  	 
				    <h5 class="card-title">{this.props.entry.title}</h5>
				    <p class="card-text">{this.props.entry.content}</p>

				  </div>
				</div>

			</div>
		)
	}
}
export default Entry;
