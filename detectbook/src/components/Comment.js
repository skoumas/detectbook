import React from 'react';

import './Comment.css';

class Comment extends React.Component {

	constructor(props) {
		super();
		this.state = {
			edit: false,
			editTitle: "",
			editContent: ""
		}
	}

	componentDidMount(props) {

	}

	edit() {
		this.setState({
		});
	}

	editSubmit() {

	}

	delete() {

	}

	render() {
		return (
			<div className="comment">
				<p>
					{ this.props.comment.content }
				</p>
				<small>
					{ this.props.comment.created_at }
				</small>
			</div>
		)
	}
}
export default Comment;
