import React, { Component } from 'react';
import './Entry.css';

class User extends React.Component {

	constructor(props) {
		super();
	}

	componentDidMount(props) {
	}

	render() {
		return (
			<div className="user">
 				{this.props.user.name}
			</div>
		)
	}
}
export default User;
