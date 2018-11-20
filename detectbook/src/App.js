import React, { Component } from 'react';
import logo from './logo.svg';
import Entry from './components/Entry';
import './App.css';

class App extends Component {

	constructor(props) {
		super(props);
		this.state = {
			entries: [],
			page: "guestbook",
			logged: false,
			errorMessage: "",
			loginEmail: null,
			loginPassword: null,
			user: null
		};
		this.login = this.login.bind(this);
		this.loginSubmit = this.loginSubmit.bind(this);
		this.entryCreate = this.entryCreate.bind(this);
		this.entrySubmit = this.entrySubmit.bind(this);
		this.logOut = this.logOut.bind(this);
	}

	checkToken() {
		let token = localStorage.getItem('token');
		fetch("http://0.0.0.0:8100/api/checkToken",{
			method: "POST",
			body: JSON.stringify({
				"token": token
			})
		})
		.then(res => res.json())
		.then(
			(result) => {
				if (result.success) {
					this.setState({
						logged: true,
						user: result.user,
						page: "guestbook"
					});
				} else {
					localStorage.removeItem('token');
					this.setState({
						logged: false
					});
				}
			},
			(error) => {
				this.setState({

				});
			}
		)
	}

	componentDidMount() {
		if (localStorage.getItem('token')) {
			this.checkToken();
		}
		fetch("http://0.0.0.0:8100/api/guestbook/1/entries")
			.then(res => res.json())
			.then(
			(result) => {
				this.setState({
					entries: result
				});
			},
			(error) => {
				this.setState({

				});
			}
		)
	}


	login() {
		this.setState({
			page: "login"
		});
	}

	logOut() {
		localStorage.removeItem('token');
		this.setState({
			token: null,
			logged:false,
			page:"guestbook"
		});
	}

	loginSubmit() {
		console.log({"email":this.state.loginEmail,"password":this.state.loginPassword});
		fetch("http://0.0.0.0:8100/api/user/login",{
			method: "POST",
			body: JSON.stringify({
				"email":this.state.loginEmail,
				"password":this.state.loginPassword
			})
		})
		.then(res => res.json())
		.then(
			(result) => {
				if (result.success) {
					this.setState({
						token: result.token
					});
					localStorage.setItem('token', result.token);
					this.checkToken();
				} else {
					this.setState({
						errorMessage: "Sorry wrong credentials"
					});
				}
			},
			(error) => {
				this.setState({

				});
			}
		)
	}

	entryCreate() {
		this.setState({
			page: "entryCreate"
		});
	}


	entrySubmit() {
		console.log({"email":this.state.loginEmail,"password":this.state.loginPassword});
		fetch("http://0.0.0.0:8100/api/entry/create",{
			method: "POST",
			body: JSON.stringify({
				"title":this.state.entryTitle,
				"content":this.state.entryContent,
				"user_id":this.state.user.id
			})
		})
		.then(res => res.json())
		.then(
			(result) => {
				if (result.success) {


				} else {

				}
			},
			(error) => {
				this.setState({

				});
			}
		)
	}


	render() {
		return (
			<div className="App">
				<div className="container">

					{this.state.logged &&
						<div align="center">
							<h1 align="center"> Welcome back {this.state.user.email } </h1>
							<button onClick={this.logOut} className="btn-xs btn btn-danger">Logout</button>
						</div>
					}
					{!this.state.logged &&
						<div align="center">
							<h1 > Guestbook </h1>

						</div>
					}
					<br/>
					{this.state.errorMessage &&
						<div className="alert alert-danger">
							{this.state.errorMessage}
						</div>
					}

					{( this.state.page=="guestbook" || this.state.page=="entryCreate") &&
						<div>
							{this.state.entries.map((entry, i) => {
								return (<Entry entry={entry}></Entry>)
							})}
							<br/>
							{!this.state.logged &&
								<div align="center">
									<h2> Write your impressions!</h2>
									You must be logged in to write a comment!<br/><br/>
									<button onClick={this.login} className="btn-xs btn btn-success">Login</button>
								</div>
							}

						</div>
					}

					
					{this.state.page=="login" &&
						<div>
							<div className="form-group">
						  		<label for="exampleInputPassword1">Email</label>
								<input type="text" className="form-control" name="email"  value={this.state.loginEmail}  onChange={ (e) => this.setState({ loginEmail: e.target.value }) }/>
						 	</div>

							<div className="form-group">
								<label for="exampleInputPassword1">Password</label>
								<input type="text" className="form-control" name="password" value={this.state.loginPassword} onChange={ (e) => this.setState({ loginPassword: e.target.value }) }/>
							</div>

							<button onClick={this.loginSubmit} className="btn btn-primary" type="submit">Submit</button>
						</div>
					}

					{this.state.logged &&
						<div className="entryCreate">
							<h2> Write your impressions!</h2>
							<div className="form-group">
								<label for="exampleInputPassword1">Title</label>
								<input type="text" className="form-control" name="email"  value={this.state.entryTitle}  onChange={ (e) => this.setState({ entryTitle: e.target.value }) }/>
							</div>

							<div className="form-group">
								<label for="exampleInputPassword1">Comment</label>
								<input type="text" className="form-control" name="password" value={this.state.entryContent} onChange={ (e) => this.setState({ entryContent: e.target.value }) }/>
							</div>

							<button onClick={this.entrySubmit} className="btn btn-primary" type="submit">Submit</button>
						</div>
					}
				</div>
			</div>
		);
	}
}
export default App;
