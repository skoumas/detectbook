import React, { Component } from 'react';

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
			loginEmail: "",
			loginPassword: "",
			signupName: "",
			signupEmail: "",
			signupPassword: "",
			user: null
		};
		this.login = this.login.bind(this);
		this.loginSubmit = this.loginSubmit.bind(this);
		this.entrySubmit = this.entrySubmit.bind(this);
		this.signup = this.signup.bind(this);
		this.signupSubmit = this.signupSubmit.bind(this);
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
					//localStorage.removeItem('token');
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
	loadComponents() {
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
	componentDidMount() {
		if (localStorage.getItem('token')) {
			this.checkToken();
		}
		this.loadComponents();
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
					window.localStorage.setItem('token', result.token);
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

	signup() {
		this.setState({
			page: "signup"
		});
	}




	signupSubmit() {
		fetch("http://0.0.0.0:8100/api/user/create",{
			method: "POST",
			body: JSON.stringify({
				"name":this.state.signupName,
				"email":this.state.signupEmail,
				"password":this.state.signupPassword
			})
		})
		.then(res => res.json())
		.then(
			(result) => {
				if (result.success) {
					this.setState({
						page:"guestbook"
					});
				} else {

				}
			},
			(error) => {
				this.setState({

				});
			}
		)
	}

	entrySubmit() {
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
					this.loadComponents();
					this.setState({
						page:"guestbook"
					});
				} else {

				}
			},
			(error) => {
				this.setState({
					errorMessage: "Something went wrong. Please try again"
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
							<h1 align="center"> Oh hi {this.state.user.name } | <button onClick={this.logOut} className="btn-xs btn btn-danger">Logout</button></h1>

						</div>
					}
					{!this.state.logged &&
						<div align="center">
							<h1> Guestbook </h1>
						</div>
					}
					<br/>
					{this.state.errorMessage &&
						<div className="alert alert-danger">
							{this.state.errorMessage}
						</div>
					}

					{( this.state.page==="guestbook" || this.state.page==="entryCreate") &&
						<div>
							{this.state.entries.map((entry, i) => {
								return (<Entry app={this} key={i} entry={entry} user={this.state.user}></Entry>)
							})}
							<br/>
							{!this.state.logged &&
								<div align="center">
									<h2> Write your impressions!</h2>
									You must be logged in to write a comment!<br/><br/>
									<button onClick={this.login} className="btn-xs btn btn-success">Login</button>
									<button onClick={this.signup} className="btn-xs btn btn-success">Sign-up</button>
								</div>
							}

						</div>
					}


					{this.state.page==="login" &&
						<div>
							<h2> Login!</h2>
							<div className="form-group">
						  		<label htmlFor="exampleInputPassword1">Email</label>
								<input type="text" className="form-control" name="email"  value={this.state.loginEmail}  onChange={ (e) => this.setState({ loginEmail: e.target.value }) }/>
						 	</div>

							<div className="form-group">
								<label htmlFor="exampleInputPassword1">Password</label>
								<input type="text" className="form-control" name="password" value={this.state.loginPassword} onChange={ (e) => this.setState({ loginPassword: e.target.value }) }/>
							</div>

							<button onClick={this.loginSubmit} className="btn btn-primary" type="submit">Submit</button>
						</div>
					}

					{this.state.page==="signup" &&
						<div>
							<h2> Sign-up!</h2>
							<div className="form-group">
								<label htmlFor="exampleInputPassword1">Name</label>
								<input type="text" className="form-control"  value={this.state.signupName} onChange={ (e) => this.setState({ signupName: e.target.value }) }/>
							</div>

							<div className="form-group">
						  		<label htmlFor="exampleInputPassword1">Email</label>
								<input type="text" className="form-control"    value={this.state.signupEmail}  onChange={ (e) => this.setState({ signupEmail: e.target.value }) }/>
						 	</div>

							<div className="form-group">
								<label htmlFor="exampleInputPassword1">Password</label>
								<input type="text" className="form-control"   value={this.state.signupPassword} onChange={ (e) => this.setState({ signupPassword: e.target.value }) }/>
							</div>

							<button onClick={this.signupSubmit} className="btn btn-primary" type="submit">Submit</button>
						</div>
					}

					{this.state.logged &&
						<div className="entryCreate">
							<h2> Write your impressions!</h2>
							<div className="form-group">
								<label htmlFor="exampleInputPassword1">Title</label>
								<input type="text" className="form-control" name="email"  value={this.state.entryTitle}  onChange={ (e) => this.setState({ entryTitle: e.target.value }) }/>
							</div>

							<div className="form-group">
								<label htmlFor="exampleInputPassword1">Comment</label>
								<textarea  className="form-control"    onChange={ (e) => this.setState({ entryContent: e.target.value }) }>
									{this.state.entryContent}
								</textarea>
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
